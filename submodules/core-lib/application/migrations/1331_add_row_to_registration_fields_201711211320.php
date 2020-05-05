<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_to_registration_fields_201711211320 extends CI_Migration {

	public function up() {

		// Insert into registration_fields
        $data = array(
            array(
                'registrationFieldId' => 48,
                'type' => '1',
                'field_name' => 'Zip Code',
                'alias' => 'zipCode',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-11-21 13:30:00',
                'can_be_required' => '0',
            ),
            array(
                'registrationFieldId' => 49,
                'type' => '1',
                'field_name' => 'ID Card Number',
                'alias' => 'idCardNumber',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-11-21 13:30:00',
                'can_be_required' => '0',
            ),
        );
        $this->db->insert_batch('registration_fields', $data);

	}

	public function down() {
        $this->db->where('alias', array('zipCode','idCardNumber'));
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');
	}
}