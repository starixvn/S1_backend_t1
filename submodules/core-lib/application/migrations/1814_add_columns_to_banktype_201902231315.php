<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_banktype_201902231315 extends CI_Migration {

    public function up() {
        $fields = [
            'payment_type_flag' => [
                'type' => 'TINYINT',
                'null' => FALSE,
                'default' => 1
            ]
        ];

        if(!$this->db->field_exists('payment_type_flag', 'banktype')){
            $this->dbforge->add_column('banktype', $fields);

            $this->db->set('payment_type_flag', 4);
            $this->db->where('external_system_id >', 0);
            $this->db->update('banktype');
        }
    }

    public function down() {
        if($this->db->field_exists('payment_type_flag', 'banktype')){
            $this->dbforge->drop_column('banktype', 'payment_type_flag');
        }
    }
}
