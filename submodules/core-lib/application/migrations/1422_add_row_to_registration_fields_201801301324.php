<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_to_registration_fields_201801301324 extends CI_Migration {

    private $tableName = 'registration_fields';

	public function up() {

        $data = array(
            'registrationFieldId' => 51,
            'type' => '1',
            'field_name' => 'ID Card Type',
            'alias' => 'id_card_type',
            'visible' => '1',
            'required' => '1',
            'updatedOn' => date("Y-m-d H:i:s"),
            'can_be_required' => '0',
        );
        $this->db->insert($this->tableName, $data);
 
	}

	public function down() {
        $this->db->where('alias', array('id_card_type'));
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);
	}
}