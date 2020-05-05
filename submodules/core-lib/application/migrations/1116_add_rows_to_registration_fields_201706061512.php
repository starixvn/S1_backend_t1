<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_registration_fields_201706061512 extends CI_Migration {

    public function up() {
        $data = array(
            array(
                'registrationFieldId' => 43,
                'type' => '1',
                'field_name' => 'Address2',
                'alias' => 'address2',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-06-06 15:12:00',
                'can_be_required' => '0',
            ),
            array(
                'registrationFieldId' => 44,
                'type' => '1',
                'field_name' => 'Address3',
                'alias' => 'address3',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-06-06 15:12:00',
                'can_be_required' => '0',
            ),
        );
        $this->db->insert_batch('registration_fields', $data);
    }

    public function down() {
        $this->db->where_in('field_name', array('Address2','Address3'));
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');
    }
}