<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_aff_email_to_registration_fields_201812261600 extends CI_Migration {

    private $tableName = 'registration_fields';

    public function up() {
        $data = array(
            'type' => '2',
            'field_name' => 'Email Address',
            
        );
        
        $this->db->insert($this->tableName, $data);
    }

    public function down() {
        $this->db->where('type', '2');
        $this->db->where('field_name', 'Email Address');
        $this->db->delete($this->tableName);
    }
}