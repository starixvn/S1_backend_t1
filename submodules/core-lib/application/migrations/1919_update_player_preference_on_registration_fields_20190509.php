<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_player_preference_on_registration_fields_20190509 extends CI_Migration {

	private $tableName = 'registration_fields';

	public function up() {

		$data = array(
            array(
                'registrationFieldId' => 54,
                'account_visible' => '1',
                'account_required' => '1',
            ),
            array(
                'registrationFieldId' => 55,
                'account_visible' => '1',
                'account_required' => '1',
            ),
            array(
                'registrationFieldId' => 56,
                'account_visible' => '1',
                'account_required' => '1',
            ),
            array(
                'registrationFieldId' => 57,
                'account_visible' => '1',
                'account_required' => '1',
            ),
            array(
                'registrationFieldId' => 58,
                'account_visible' => '1',
                'account_required' => '1',
            ),
        );

        $this->db->update_batch($this->tableName, $data, 'registrationFieldId');

	}

	public function down() {}
}