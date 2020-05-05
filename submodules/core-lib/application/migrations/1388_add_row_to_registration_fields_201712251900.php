<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_to_registration_fields_201712251900 extends CI_Migration {

    private $tableName = 'registration_fields';

	public function up() {

        $data = array(
            'registrationFieldId' => 50,
            'type' => '1',
            'field_name' => 'Dialing Code',
            'alias' => 'dialing_code',
            'visible' => '1',
            'required' => '1',
            'updatedOn' => date("Y-m-d H:i:s"),
            'can_be_required' => '0',
        );
        $this->db->insert($this->tableName, $data);
 
	}

	public function down() {
        $this->db->where('alias', array('dialing_code'));
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);
	}
}