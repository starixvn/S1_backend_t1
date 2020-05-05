<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_field_order_on_registration_fields_20190510 extends CI_Migration {

    private $tableName = 'registration_fields';

    public function up()
    {
        # Add column
        $fields = array(
            'field_order' => array(
                'type' => 'INT'
            )
        );


        if(!$this->db->field_exists('field_order', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'registrationFieldId');
            $this->db->set('field_order', 'registrationFieldId', false);
            $this->db->update($this->tableName);
        }
    }

    public function down()
    {
        if($this->db->field_exists('field_order', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'field_order', 'registrationFieldId');
        }
    }
}
