<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_to_messages_201902141732 extends CI_Migration {

    public function up() {
        $fields = [
            'message_type' => [
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => true,
                'default' => 0
            ]
        ];

        if(!$this->db->field_exists('message_type', 'messages')){
            $this->dbforge->add_column('messages', $fields);
        }
    }

    public function down() {
        if($this->db->field_exists('message_type', 'messages')){
            $this->dbforge->drop_column('messages', 'message_type');
        }
    }
}
