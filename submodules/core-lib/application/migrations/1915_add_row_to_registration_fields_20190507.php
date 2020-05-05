<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_row_to_registration_fields_20190507 extends CI_Migration {

    private $tableName = 'registration_fields';

	public function up() {

        $this->db->where('alias', 'player_preference');
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);

        $this->db->where('alias', 'player_preference_email');
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);

        $this->db->where('alias', 'player_preference_sms');
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);

        $this->db->where('alias', 'player_preference_phone_call');
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);

        $this->db->where('alias', 'player_preference_post');
        $this->db->where('type', '1');
        $this->db->delete($this->tableName);

        $data = array(
            array(
                'registrationFieldId' => 54,
                'type' => '1',
                'field_name' => 'Player Preference',
                'alias' => 'player_preference',
                'required' => '1',
                'updatedOn' => date("Y-m-d H:i:s"),
            ),
            array(
                'registrationFieldId' => 55,
                'type' => '1',
                'field_name' => 'Player Preference Email',
                'alias' => 'player_preference_email',
                'required' => '1',
                'updatedOn' => date("Y-m-d H:i:s"),
            ),
            array(
                'registrationFieldId' => 56,
                'type' => '1',
                'field_name' => 'Player Preference SMS',
                'alias' => 'player_preference_sms',
                'required' => '1',
                'updatedOn' => date("Y-m-d H:i:s"),
            ),
            array(
                'registrationFieldId' => 57,
                'type' => '1',
                'field_name' => 'Player Preference Phone Call',
                'alias' => 'player_preference_phone_call',
                'required' => '1',
                'updatedOn' => date("Y-m-d H:i:s"),
            ),
            array(
                'registrationFieldId' => 58,
                'type' => '1',
                'field_name' => 'Player Preference Post',
                'alias' => 'player_preference_post',
                'required' => '1',
                'updatedOn' => date("Y-m-d H:i:s"),
            ),
        );

        $this->db->insert_batch('registration_fields', $data);
	}

	public function down() {}
}
