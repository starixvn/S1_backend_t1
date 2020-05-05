<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_rows_to_registration_fields_tables_201711211508 extends CI_Migration {

    public function up() {

        $this->db->where('alias', 'zipCode');
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');

        $this->db->where('alias', 'idCardNumber');
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');

        $data = array(
             array(
                'registrationFieldId' => 48,
                'type' => '1',
                'field_name' => 'Zip Code',
                'alias' => 'zipcode',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-11-21 13:30:00',
                'can_be_required' => '0',
            ),
            array(
                'registrationFieldId' => 49,
                'type' => '1',
                'field_name' => 'ID Card Number',
                'alias' => 'id_card_number',
                'visible' => '1',
                'required' => '1',
                'updatedOn' => '2017-11-21 13:30:00',
                'can_be_required' => '0',
            ),
        );
        $this->db->insert_batch('registration_fields', $data);
    }

    public function down() {
        /*$this->db->where('alias', array('id_card_number'));
        $this->db->where('type', '1');
        $this->db->delete('registration_fields');*/
    }

}
