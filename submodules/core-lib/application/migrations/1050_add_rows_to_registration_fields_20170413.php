<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_rows_to_registration_fields_20170413 extends CI_Migration {

    public function up() {
        $data = array(
            array(
                'registrationFieldId' => 40,
                'type' => '1',
                'field_name' => 'Bank Name',
                'alias' => 'bankName',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-04-13 15:00:00',
                'can_be_required' => '0',
            ),
            array(
                'registrationFieldId' => 41,
                'type' => '1',
                'field_name' => 'Bank Account Number',
                'alias' => 'bankAccountNumber',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-04-13 15:00:00',
                'can_be_required' => '0',
            ),
            array(
                'registrationFieldId' => 42,
                'type' => '1',
                'field_name' => 'Bank Account Name',
                'alias' => 'bankAccountName',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-04-13 15:00:00',
                'can_be_required' => '0',
            ),
        );
        $this->db->insert_batch('registration_fields', $data);
    }

    public function down() {
        $this->db->where_in('field_name', array('Bank Name','Bank Account Number','Bank Account Name'));
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');
    }
}