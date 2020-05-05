<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_messagedetails_201812041931 extends CI_Migration {

    public function up() {
        $fields = [
            'adminId' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'default' => 0,
                'null' => false,
            ],
        ];

        if(!$this->db->field_exists('adminId', 'messagesdetails')){
            $this->dbforge->add_column('messagesdetails', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('adminId', 'messagesdetails')){
            $this->dbforge->drop_column('messagesdetails', 'adminId');
        }
    }
}
