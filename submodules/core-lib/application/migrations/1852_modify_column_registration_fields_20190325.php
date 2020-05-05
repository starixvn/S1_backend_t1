<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_modify_column_registration_fields_20190325 extends CI_Migration {

    private $tableName = 'registration_fields';

    public function up()
    {
        # Close column: id_card_type
        $this->db->where('registrationFieldId', 51);
        $this->db->update($this->tableName, [
            'visible'  => '1',
            'required' => '1',
            'account_visible' => '1',
            'account_visible' => '1',
        ]);

        # Close column: id_card_number
        $this->db->where('registrationFieldId', 49);
        $this->db->update($this->tableName, [
            'account_visible' => '1',
            'account_visible' => '1',
        ]);
    }

    public function down(){}
}
